import * as React from 'react';
import { ApolloError } from '@apollo/client';
import { useSamplePaginationQuery } from '@/script/Graphql/codegen/graphql';
import { IOffsetPagination } from '@/script/Hooks/IOffsetPagination';
import {
  AppTrnUserList,
  IAppTrnUser,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';

export interface IAppPagination {
  pagination: IOffsetPagination;
}

export function useFetchSamplePagination(
  fetchParameter: IAppPagination,
): [
  boolean,
  number,
  AppTrnUserList,
  (pagination: IOffsetPagination) => void,
  ApolloError | undefined,
] {
  const [cursor, setCursor] = React.useState<IOffsetPagination>({
    pageIndex: fetchParameter.pagination.pageIndex,
    pageStep: fetchParameter.pagination.pageStep,
  });

  /**
   * クエリ実施.
   */
  const query = useSamplePaginationQuery({
    variables: {
      pageIndex: cursor.pageIndex,
      pageStep: cursor.pageStep,
    },
    fetchPolicy: 'no-cache',
  });

  // データのparse処理.
  const list = new AppTrnUserList(
    query.data?.SamplePagination.list.map(v =>
      parseAppTrnUserPayload(v as IAppTrnUser),
    ),
  );
  const count = query.data?.SamplePagination.count ?? 0;

  // カーソル変更処理.
  const changeCursor = (pagination: IOffsetPagination) => {
    setCursor(pagination);
  };

  return [query.loading, count, list, changeCursor, query.error];
}
