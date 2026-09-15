import * as React from 'react';
import { DocumentNode, useApolloClient } from '@apollo/client';
import { IMutationResult, parseErrors } from '@/script/Hooks/IMutationResult';
import { useAppPersonalSettingShowAlignSlackMutation } from '@/script/Graphql/codegen/graphql';
import { E_STATUS_CODE } from '@/script/Enum/Server/App/EStatusCode';

export function useMutationAppPersonalSettingShowAlignSlack(): [
  boolean,
  (refetchQueries: DocumentNode[]) => Promise<IMutationResult>,
] {
  const [sending, setSending] = React.useState<boolean>(false);
  const [mutation] = useAppPersonalSettingShowAlignSlackMutation({});
  const client = useApolloClient();

  /**
   * 送信関数
   */
  const mutationAppPersonalSettingShowAlignSlack = async (
    refetchQueries: DocumentNode[],
  ): Promise<IMutationResult> => {
    setSending(true);

    return new Promise<IMutationResult>(resolve => {
      mutation().then(async response => {
        const statusCode = Number(
          response.data?.AppPersonalSettingShowAlignSlack?.statusCode,
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
            response.data?.AppPersonalSettingShowAlignSlack?.statusMessage,
          ),
          errors: parseErrors(
            response.data?.AppPersonalSettingShowAlignSlack?.errors,
          ),
        });
      });
    });
  };

  return [sending, mutationAppPersonalSettingShowAlignSlack];
}
