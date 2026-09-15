import * as React from 'react';
import { DocumentNode, useApolloClient } from '@apollo/client';
import { IMutationResult, parseErrors } from '@/script/Hooks/IMutationResult';
import { useAppPersonalSettingSpConfirmCodeMutation } from '@/script/Graphql/codegen/graphql';
import { E_STATUS_CODE } from '@/script/Enum/Server/App/EStatusCode';

export interface IAppPersonalSettingSpConfirmCode {
  code: string;
}

export function useMutationAppPersonalSettingSpConfirmCode(): [
  boolean,
  (
    props: IAppPersonalSettingSpConfirmCode,
    refetchQueries: DocumentNode[],
  ) => Promise<IMutationResult>,
] {
  const [sending, setSending] = React.useState<boolean>(false);
  const [mutation] = useAppPersonalSettingSpConfirmCodeMutation({});
  const client = useApolloClient();

  /**
   * 送信関数.
   */
  const mutationAppPersonalSettingSpConfirmCode = async (
    props: IAppPersonalSettingSpConfirmCode,
    refetchQueries: DocumentNode[],
  ): Promise<IMutationResult> => {
    setSending(true);

    return new Promise<IMutationResult>(resolve => {
      mutation({
        variables: {
          code: String(props.code),
        },
      }).then(async response => {
        const statusCode = Number(
          response.data?.AppPersonalSettingSpConfirmCode?.statusCode,
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
            response.data?.AppPersonalSettingSpConfirmCode?.statusMessage,
          ),
          errors: parseErrors(
            response.data?.AppPersonalSettingSpConfirmCode?.errors,
          ),
        });
      });
    });
  };

  return [sending, mutationAppPersonalSettingSpConfirmCode];
}
