import * as React from 'react';
import { DocumentNode, useApolloClient } from '@apollo/client';
import { IMutationResult, parseErrors } from '@/script/Hooks/IMutationResult';
import { useSampleInputFormUpdateMutation } from '@/script/Graphql/codegen/graphql';
import { E_STATUS_CODE } from '@/script/Enum/Server/App/EStatusCode';
import { Log } from '@/script/Common/Log';

export interface ISampleInputFormUpdate {
  inputSingleNumber: string;
  inputSingleNotZero: string;
  inputSingleRange: string;
  inputSingleEmail: string;
  inputMulti: string;
  inputSelectStringId: number;
}

export function useMutationSampleInputFormUpdate(): [
  boolean,
  (
    props: ISampleInputFormUpdate,
    refetchQueries: DocumentNode[],
  ) => Promise<IMutationResult>,
] {
  const [sending, setSending] = React.useState<boolean>(false);
  const [mutation] = useSampleInputFormUpdateMutation({});
  const client = useApolloClient();

  /**
   * 送信関数
   */
  const mutationSampleInputFormUpdate = async (
    props: ISampleInputFormUpdate,
    refetchQueries: DocumentNode[],
  ): Promise<IMutationResult> => {
    setSending(true);

    return new Promise<IMutationResult>(resolve => {
      mutation({
        variables: {
          ...props,
        },
      })
        .then(async response => {
          const statusCode = Number(
            response.data?.SampleInputFormUpdate?.statusCode,
          );

          if (statusCode === E_STATUS_CODE.OK && refetchQueries.length) {
            await client.refetchQueries({
              include: refetchQueries,
            });
          }

          // プロセス終了を通知.
          setSending(false);

          resolve({
            statusCode,
            statusMessage: String(
              response.data?.SampleInputFormUpdate?.statusMessage,
            ),
            errors: parseErrors(response.data?.SampleInputFormUpdate?.errors),
          });
        })
        .catch(_error => {
          Log.error(_error);
          setSending(false);
        });
    });
  };

  return [sending, mutationSampleInputFormUpdate];
}
